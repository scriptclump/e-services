/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inbound_requests', {
    inbound_request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    lp_request_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    client_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    seller_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    po_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    inbound_request_type: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    request_status: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    scheduling_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    stn: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    srid: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '4'
    },
    is_cancelled: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    po_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inbound_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    discount_per: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    shiping_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
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
    tableName: 'inbound_requests'
  });
};
