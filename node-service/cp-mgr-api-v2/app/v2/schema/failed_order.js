/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('failed_order', {
    failed_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(256),
      allowNull: false
    },
    order_data: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    order_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    cus_mobile_no: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    is_processed: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '185003'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    tableName: 'failed_order'
  });
};
