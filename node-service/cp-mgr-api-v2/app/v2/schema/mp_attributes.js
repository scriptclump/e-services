/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp_attributes', {
    mp_att_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_key: {
      type: DataTypes.STRING(4),
      allowNull: true
    },
    feature_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    feature_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    feature_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    isfilterfeature: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    isrequiredfeature: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    deleted_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
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
    tableName: 'mp_attributes'
  });
};
