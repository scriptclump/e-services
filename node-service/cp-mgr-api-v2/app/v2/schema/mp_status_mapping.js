/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp_status_mapping', {
    mp_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'mp',
        key: 'mp_id'
      }
    },
    status_type: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    mp_status: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    order_status_description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ebutor_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'mp_status_mapping'
  });
};
